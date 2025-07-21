import { Controller, Body, Param } from '@nestjs/common';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { TokensService } from './tokens.service';
import { CreateTokenDto } from './dtos/create-token.dto';
import { ApiTags } from '@nestjs/swagger';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { ReadTokenDto } from '@/tokens/dtos/read-token.dto';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { CreatedTokenDto } from '@/tokens/dtos/created-token.dto';
import { UsersService } from '@/users/users.service';
import { User } from '@/users/types/user';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { SessionAuth } from '@/auth/session/session-auth.decorator';

@ApiTags('Tokens')
@Controller('api/tokens')
export class TokensController {
	constructor(
		private readonly usersService: UsersService,
		private readonly tokensService: TokensService,
	) {}

	@GetMany({
		name: 'Tokens',
		summary: 'Get the tokens',
		operationId: 'getTokens',
		responseType: [ReadTokenDto],
	})
	@SessionAuth()
	async getTokens(@AuthUser() user: User): Promise<ReadTokenDto[]> {
		const tokens = await this.tokensService.getTokens(user);

		const tokensDto = tokens.map((token) => new ReadTokenDto(token));

		return tokensDto;
	}

	@GetOne({
		name: 'Token',
		summary: 'Get the specified token',
		operationId: 'getToken',
		responseType: ReadTokenDto,
	})
	@SessionAuth()
	async getToken(
		@Param('id') id: string,
		@AuthUser() user: User,
	): Promise<ReadTokenDto> {
		const token = await this.tokensService.getToken(id, user);

		return new ReadTokenDto(token);
	}

	@CustomPost({
		name: 'Token',
		summary: 'Create a new token',
		bodyType: CreateTokenDto,
		responseType: CreatedTokenDto,
		operationId: 'createToken',
	})
	@SessionAuth()
	async createToken(
		@AuthUser() user: User,
		@Body() createTokenDto: CreateTokenDto,
	): Promise<CreatedTokenDto> {
		const createdToken = await this.tokensService.createToken(
			{ ...createTokenDto },
			user,
		);

		return new CreatedTokenDto(createdToken);
	}

	@CustomDelete({
		name: 'Token',
		summary: 'Delete the specified token',
		operationId: 'deleteToken',
	})
	@SessionAuth()
	async deleteToken(
		@Param('id') id: string,
		@AuthUser() user: User,
	): Promise<void> {
		await this.tokensService.deleteToken(id, user);
	}
}
