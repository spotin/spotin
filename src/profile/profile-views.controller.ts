import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtOrUnrestrictedAuth } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { ProfileWithPublicSpots } from '@/profile/types/profile-with-public-spots';
import { User } from '@/users/types/user';
import { UsersService } from '@/users/users.service';
import { Controller, Get, Param, Render, UseFilters } from '@nestjs/common';
import { ApiOperation, ApiOkResponse, ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller('profile')
@UseFilters(UnauthorizedViewExceptionFilter)
export class ProfileViewsController {
	constructor(private readonly usersService: UsersService) {}

	@Get()
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the my profile page',
		description: 'Render the my profile page.',
		operationId: 'renderMyProfile',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('profile/edit')
	renderMyProfile(@AuthUser() user: User): Record<string, string> {
		return {
			title: 'ui.profile.edit.title',
			description: 'ui.profile.edit.description',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}

	@Get(':username')
	@JwtOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the specified profile with their public spots page',
		description:
			'Render the specified profile with their public spots profile page.',
		operationId: 'renderProfile',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('profile/view')
	async renderProfile(
		@AuthUser() user: User,
		@Param('username') username: string,
	): Promise<Record<string, string | ProfileWithPublicSpots>> {
		const profileWithPublicSpots =
			await this.usersService.getUserWithPublicSpotsByUsername(username);

		return {
			title: 'ui.profile.view.title',
			description: `ui.profile.view.description`,
			username: user?.username,
			email: user?.email,
			role: user?.role,
			profile: profileWithPublicSpots,
		};
	}
}
