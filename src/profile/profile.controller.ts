import { Controller, Body, Get, Patch, Param } from '@nestjs/common';
import {
	ApiBody,
	ApiConflictResponse,
	ApiNotFoundResponse,
	ApiOkResponse,
	ApiOperation,
	ApiParam,
	ApiTags,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { AuthService } from '@/auth/auth.service';
import { UsersService } from '@/users/users.service';
import { AuthJwtPayload } from '@/auth/decorators/auth-jwt-payload.decorator';
import { JwtAccessTokenAuth } from '@/auth/jwt/jwt-access-token-auth.decorator';
import { ReadProfileDto } from '@/profile/dtos/read-profile.dto';
import { UpdateProfileDto } from '@/profile/dtos/update-profile.dto';
import { ReadProfileWithPublicSpotsDto } from '@/profile/dtos/read-profile-with-public-spots.dto';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';

@ApiTags('Profile')
@Controller('api/profile')
export class ProfileController {
	constructor(
		private readonly authService: AuthService,
		private readonly usersService: UsersService,
	) {}

	@Get()
	@ApiOperation({
		summary: 'Get the current authenticated user',
		description: 'Get the current authenticated user.',
		operationId: 'getProfile',
	})
	@ApiOkResponse({
		description: `The user has been successfully retrieved.`,
		type: ReadProfileDto,
	})
	@JwtAccessTokenAuth()
	async getProfile(
		@AuthJwtPayload() { sub: userId }: JwtPayload,
	): Promise<ReadProfileDto> {
		const user = await this.usersService.getUser(userId);

		return new ReadProfileDto(user);
	}

	@Patch()
	@ApiOperation({
		summary: 'Update the current authenticated user',
		description: 'Update the current authenticated user.',
		operationId: 'updateProfile',
	})
	@ApiBody({
		description: "The user's details.",
		type: UpdateProfileDto,
	})
	@ApiOkResponse({
		description: `The user has been successfully updated.`,
		type: ReadProfileDto,
	})
	@ApiConflictResponse({
		description: 'Another user is in conflict with this one.',
	})
	@ApiUnauthorizedResponse({
		description: 'The current password is incorrect.',
	})
	@JwtAccessTokenAuth()
	async updateProfile(
		@AuthJwtPayload() payload: JwtPayload,
		@Body() profileDto: UpdateProfileDto,
	): Promise<ReadProfileDto> {
		const user = await this.usersService.getUser(payload.sub);

		await this.authService.validateCredentials({
			email: user.email,
			password: profileDto.currentPassword,
		});

		const updatedProfile = await this.usersService.updateUser(user.id, {
			...profileDto,
			password: profileDto.newPassword,
		});

		return new ReadProfileDto(updatedProfile);
	}

	@Get(':username')
	@ApiOperation({
		summary: 'Get the specified profile with their public spots',
		description: 'Get the specified profile with their public spots.',
		operationId: 'getProfileWithSpots',
	})
	@ApiParam({
		name: 'username',
		description: "The user's username.",
	})
	@ApiOkResponse({
		description: `The profile has been successfully retrieved.`,
		type: ReadProfileWithPublicSpotsDto,
	})
	@ApiNotFoundResponse({
		description: `Profile has not been found.`,
	})
	async getProfileWithSpots(
		@Param('username') username: string,
	): Promise<ReadProfileWithPublicSpotsDto> {
		const profileWithPublicSpots =
			await this.usersService.getUserWithPublicSpotsByUsername(username);

		return new ReadProfileWithPublicSpotsDto(profileWithPublicSpots);
	}
}
