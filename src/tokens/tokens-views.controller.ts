import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { TokensService } from '@/tokens/tokens.service';
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
import { User } from '@prisma/client';

@ApiTags('Views')
@Controller('tokens')
@UseFilters(UnauthorizedViewExceptionFilter)
export class TokensViewsController {
	constructor(private readonly tokensService: TokensService) {}

	@Get('create')
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the create a new token page',
		description: 'Render the create a new token page.',
		operationId: 'renderCreateToken',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('tokens/form')
	renderCreateToken(@AuthUser() user: User) {
		return {
			title: 'Create a new token | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
		};
	}

	@Get()
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the tokens page',
		description: 'Render the tokens page.',
		operationId: 'renderTokensList',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('tokens/list')
	async renderTokensList(@AuthUser() user: User) {
		const tokens = await this.tokensService.getTokens(user);

		return {
			title: 'Tokens | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
			tokens,
		};
	}

	@Get(':id/delete')
	@JwtAuth()
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
	async deleteToken(@AuthUser() user: User, @Param('id') id: string) {
		await this.tokensService.deleteToken(id, user);
	}

	@Get(':id')
	@JwtAuth()
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
	async renderToken(@AuthUser() user: User, @Param('id') id: string) {
		const token = await this.tokensService.getToken(id, user);

		return {
			title: 'Token | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
			token,
		};
	}
}
