import { AuthJwtPayload } from '@/auth/decorators/auth-jwt-payload.decorator';
import { JwtAccessTokenOrUnrestrictedAuth } from '@/auth/jwt-access-token-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { JwtAccessTokenAuth } from '@/auth/jwt/jwt-access-token-auth.decorator';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { ProfileWithPublicSpots } from '@/profile/types/profile-with-public-spots';
import { UsersService } from '@/users/users.service';
import { Controller, Get, Param, Render, UseFilters } from '@nestjs/common';
import { ApiOperation, ApiOkResponse, ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller('profile')
@UseFilters(UnauthorizedViewExceptionFilter)
export class ProfileViewsController {
	constructor(private readonly usersService: UsersService) {}

	@Get()
	@JwtAccessTokenAuth()
	@ApiOperation({
		summary: 'Render the my profile page',
		description: 'Render the my profile page.',
		operationId: 'renderMyProfile',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('profile/form')
	async renderMyProfile(
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string>> {
		const user = await this.usersService.getUser(payload.sub);

		return {
			title: 'My profile | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}

	@Get(':username')
	@JwtAccessTokenOrUnrestrictedAuth()
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
		@AuthJwtPayload() payload: JwtPayload | undefined,
		@Param('username') username: string,
	): Promise<Record<string, string | ProfileWithPublicSpots>> {
		const profileWithPublicSpots =
			await this.usersService.getUserWithPublicSpotsByUsername(username);

		if (payload?.sub) {
			const user = await this.usersService.getUser(payload.sub);

			return {
				title: 'Profile | Spot in',
				username: user.username,
				email: user.email,
				role: user.role,
				profile: profileWithPublicSpots,
			};
		}

		return {
			title: 'Profile | Spot in',
		};
	}
}
