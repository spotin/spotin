import { Controller, Body, Get, Patch } from '@nestjs/common';
import {
	ApiBody,
	ApiConflictResponse,
	ApiOkResponse,
	ApiOperation,
	ApiTags,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { User } from '@prisma/client';
import { AuthService } from '@/auth/auth.service';
import { UsersService } from '@/users/users.service';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { ReadProfileDto } from '@/profile/dtos/read-profile.dto';
import { UpdateProfileDto } from '@/profile/dtos/update-profile.dto';

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
	async getProfile(@AuthUser() user: User): Promise<ReadProfileDto> {
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
		await this.authService.validateCredentials({
			email: user.email,
			password: profileDto.currentPassword,
		});

		const updatedProfile = await this.usersService.updateUser(user.id, {
			username: profileDto.username,
			email: profileDto.email,
			password: profileDto.newPassword,
		});

		return new ReadProfileDto(updatedProfile);
	}
}
