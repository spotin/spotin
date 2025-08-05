import { Controller, Body, Param } from '@nestjs/common';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { TokensService } from '@/tokens/tokens.service';
import { CreateTokenDto } from '@/tokens/dtos/create-token.dto';
import { ApiTags } from '@nestjs/swagger';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { ReadTokenDto } from '@/tokens/dtos/read-token.dto';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { CreatedTokenDto } from '@/tokens/dtos/created-token.dto';
import { User } from '@/users/types/user';

@ApiTags('Tokens')
@Controller('api/tokens')
export class TokensController {
	constructor(private readonly tokensService: TokensService) {}

	@GetMany({
		name: 'Tokens',
		summary: 'Get the tokens',
		operationId: 'getTokens',
		responseType: [ReadTokenDto],
	})
	@JwtAuth()
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
	@JwtAuth()
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
	@JwtAuth()
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
	@JwtAuth()
	async deleteToken(
		@Param('id') id: string,
		@AuthUser() user: User,
	): Promise<void> {
		await this.tokensService.deleteToken(id, user);
	}
}
