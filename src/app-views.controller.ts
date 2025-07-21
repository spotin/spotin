import { AuthJwtPayload } from '@/auth/decorators/auth-jwt-payload.decorator';
import { JwtAccessTokenOrUnrestrictedAuth } from '@/auth/jwt-access-token-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { UsersService } from '@/users/users.service';
import { Get, Controller, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller()
export class AppViewsController {
	constructor(private readonly usersService: UsersService) {}

	@Get()
	@JwtAccessTokenOrUnrestrictedAuth()
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
		@AuthJwtPayload() payload: JwtPayload | undefined,
	): Promise<Record<string, string | undefined>> {
		let params: Record<string, string | undefined> = {
			title: 'Home | Spot in',
		};

		console.log('payload', payload);

		if (payload?.sub) {
			const user = await this.usersService.getUser(payload.sub);

			params = {
				...params,
				username: user.username,
				email: user.email,
				role: user.role,
			};
		}

		return params;
	}

	@Get('about')
	@JwtAccessTokenOrUnrestrictedAuth()
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
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string | undefined>> {
		let params: Record<string, string | undefined> = {
			title: 'About | Spot in',
		};

		if (payload?.sub) {
			const user = await this.usersService.getUser(payload.sub);

			params = {
				...params,
				username: user.username,
				email: user.email,
				role: user.role,
			};
		}

		return params;
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
	@JwtAccessTokenOrUnrestrictedAuth()
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
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string | undefined>> {
		let params: Record<string, string | undefined> = {
			title: 'Not Found | Spot in',
		};

		if (payload?.sub) {
			const user = await this.usersService.getUser(payload.sub);

			params = {
				...params,
				username: user.username,
				email: user.email,
				role: user.role,
			};
		}

		return params;
	}
}
