import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtOrUnrestrictedAuth } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { User } from '@/users/types/user';
import { Get, Controller, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller()
export class AppViewsController {
	@Get()
	@JwtOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the main page',
		description: 'Render the main page.',
		operationId: 'root',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('index')
	async root(
		@AuthUser() user: User | undefined,
	): Promise<Record<string, string | undefined>> {
		return {
			title: 'Home | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
		};
	}

	@Get('about')
	@JwtOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the about page',
		description: 'Render the about page.',
		operationId: 'about',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('about')
	async about(
		@AuthUser() user: User | undefined,
	): Promise<Record<string, string | undefined>> {
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
	async api(): Promise<void> {}

	@Get('not-found')
	@JwtOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the not found page',
		description: 'Render the not found page.',
		operationId: 'notFound',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('not-found')
	async notFound(
		@AuthUser() user: User | undefined,
	): Promise<Record<string, string | undefined>> {
		return {
			title: 'Not Found | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
		};
	}
}
