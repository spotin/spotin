import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { User } from '@/users/types/user';
import { Controller, Get, Render } from '@nestjs/common';
import { ApiOperation, ApiOkResponse, ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller('profile')
export class ProfileViewsController {
	@Get()
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the profile page',
		description: 'Render the profile page.',
		operationId: 'renderProfile',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('profile/form')
	async renderProfile(@AuthUser() user: User): Promise<Record<string, string>> {
		return {
			title: 'Profile | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}
}
