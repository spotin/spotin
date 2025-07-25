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
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { ReadProfileDto } from '@/profile/dtos/read-profile.dto';
import { UpdateProfileDto } from '@/profile/dtos/update-profile.dto';
import { ReadProfileWithPublicSpotsDto } from '@/profile/dtos/read-profile-with-public-spots.dto';
import { User } from '@/users/types/user';

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
	@JwtAuth()
	getProfile(@AuthUser() user: User): ReadProfileDto {
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
	@JwtAuth()
	async updateProfile(
		@AuthUser() user: User,
		@Body() profileDto: UpdateProfileDto,
	): Promise<ReadProfileDto> {
		const { currentPassword, newPassword, ...profileDtoWithoutPasswords } =
			profileDto;

		await this.authService.validateCredentials({
			email: user.email,
			password: currentPassword,
		});

		const updatedProfile = await this.usersService.updateUser(user.id, {
			...profileDtoWithoutPasswords,
			password: newPassword,
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
