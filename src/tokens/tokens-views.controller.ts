import { AuthJwtPayload } from '@/auth/decorators/auth-jwt-payload.decorator';
import { JwtAccessTokenAuth } from '@/auth/jwt/jwt-access-token-auth.decorator';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { TokensService } from '@/tokens/tokens.service';
import { Token } from '@/tokens/types/token';
import { UsersService } from '@/users/users.service';
import {
	Controller,
	Get,
	HttpStatus,
	Param,
	Redirect,
	Render,
	UseFilters,
} from '@nestjs/common';
import {
	ApiOkResponse,
	ApiOperation,
	ApiParam,
	ApiTags,
} from '@nestjs/swagger';

@ApiTags('Views')
@Controller('tokens')
@UseFilters(UnauthorizedViewExceptionFilter)
export class TokensViewsController {
	constructor(
		private readonly tokensService: TokensService,
		private readonly usersService: UsersService,
	) {}

	@Get('create')
	@JwtAccessTokenAuth()
	@ApiOperation({
		summary: 'Render the create a new token page',
		description: 'Render the create a new token page.',
		operationId: 'renderCreateToken',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('tokens/form')
	async renderCreateToken(
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string>> {
		const user = await this.usersService.getUser(payload.sub);

		return {
			title: 'Create a new token | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}

	@Get()
	@JwtAccessTokenAuth()
	@ApiOperation({
		summary: 'Render the tokens page',
		description: 'Render the tokens page.',
		operationId: 'renderTokensList',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('tokens/list')
	async renderTokensList(
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string | Token[]>> {
		const user = await this.usersService.getUser(payload.sub);

		const tokens = await this.tokensService.getTokens(user);

		return {
			title: 'Tokens | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
			tokens,
		};
	}

	@Get(':id/delete')
	@JwtAccessTokenAuth()
	@ApiOperation({
		summary: 'Delete the specified token',
		description: 'Delete the specified token. Redirect to `/tokens`.',
		operationId: 'deleteToken',
	})
	@ApiParam({
		name: 'id',
		description: 'The token ID.',
		format: 'uuid',
	})
	@ApiOkResponse({
		description: 'Redirect successful.',
	})
	@Redirect('/tokens', HttpStatus.PERMANENT_REDIRECT)
	async deleteToken(
		@AuthJwtPayload() payload: JwtPayload,
		@Param('id') id: string,
	): Promise<void> {
		const user = await this.usersService.getUser(payload.sub);

		await this.tokensService.deleteToken(id, user);
	}

	@Get(':id')
	@JwtAccessTokenAuth()
	@ApiOperation({
		summary: 'Render the token page',
		description: 'Render the token page.',
		operationId: 'renderToken',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@ApiParam({
		name: 'id',
		description: 'The spot ID.',
		format: 'uuid',
	})
	@Render('tokens/view')
	async renderToken(
		@AuthJwtPayload() payload: JwtPayload,
		@Param('id') id: string,
	): Promise<Record<string, string | Token>> {
		const user = await this.usersService.getUser(payload.sub);

		const token = await this.tokensService.getToken(id, user);

		return {
			title: 'Token | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
			token,
		};
	}
}
