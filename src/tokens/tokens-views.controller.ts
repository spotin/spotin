import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { TokensService } from '@/tokens/tokens.service';
import { Token } from '@/tokens/types/token';
import { User } from '@/users/types/user';
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
	@Render('tokens/create')
	renderCreateToken(@AuthUser() user: User): Record<string, string> {
		return {
			title: 'ui.tokens.create.title',
			description: 'ui.tokens.create.description',
			username: user.username,
			email: user.email,
			role: user.role,
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
	@Render('tokens/index')
	async renderTokensList(
		@AuthUser() user: User,
	): Promise<Record<string, string | Token[]>> {
		const tokens = await this.tokensService.getTokens(user);

		return {
			title: 'ui.tokens.index.title',
			description: 'ui.tokens.index.description',
			username: user.username,
			email: user.email,
			role: user.role,
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
	async deleteToken(
		@AuthUser() user: User,
		@Param('id') id: string,
	): Promise<void> {
		await this.tokensService.deleteToken(id, user);
	}
}
