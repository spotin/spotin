import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { SessionOrUnrestrictedAuth } from '@/auth/session-or-unrestricted/session-or-unrestricted-auth.decorator';
import { User } from '@/users/types/user';
import { UsersService } from '@/users/users.service';
import { Get, Controller, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller()
export class AppViewsController {
	constructor(private readonly usersService: UsersService) {}

	@Get()
	@SessionOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the main page',
		description: 'Render the main page.',
		operationId: 'root',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('index')
	root(@AuthUser() user: User | undefined): Record<string, string | undefined> {
		return {
			title: 'Home | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
		};
	}

	@Get('about')
	@SessionOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the about page',
		description: 'Render the about page.',
		operationId: 'about',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('about')
	about(
		@AuthUser() user: User | undefined,
	): Record<string, string | undefined> {
		return {
			title: 'About | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
		};
	}

	@Get('api')
	@ApiOperation({
		summary: 'Render the Swagger UI page',
		description: 'Render the Swagger UI page.',
		operationId: 'api',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	api(): void {}

	@Get('not-found')
	@SessionOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the not found page',
		description: 'Render the not found page.',
		operationId: 'notFound',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('not-found')
	notFound(
		@AuthUser() user: User | undefined,
	): Record<string, string | undefined> {
		return {
			title: 'Not Found | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
		};
	}
}
