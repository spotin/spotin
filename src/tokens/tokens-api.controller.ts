import { Controller, Body, Param } from '@nestjs/common';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { TokensService } from './tokens.service';
import { CreateTokenDto } from './dto/create-token.dto';
import { ApiTags } from '@nestjs/swagger';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { User } from '@prisma/client';
import { ReadTokenDto } from '@/tokens/dto/read-token.dto';
import * as crypto from 'crypto';
import { GetOne } from '@/common/decorators/get-one.decorator';

@ApiTags('Tokens - API')
@Controller('api/tokens')
export class TokensApiController {
  constructor(private readonly tokensService: TokensService) {}

  @GetMany({
    name: 'Tokens',
    summary: 'Get the tokens',
    operationId: 'getTokensApi',
    responseType: [ReadTokenDto],
  })
  @JwtAuth()
  async getTokensApi(@AuthUser() user: User) {
    const tokens = await this.tokensService.getTokens(user);

    const tokensDto = tokens.map((token) => new ReadTokenDto(token));

    return tokensDto;
  }

  @GetOne({
    name: 'Token',
    summary: 'Get the specified token',
    operationId: 'getTokenApi',
    responseType: ReadTokenDto,
  })
  @JwtAuth()
  async getTokenApi(@Param('id') id: string, @AuthUser() user: User) {
    const token = await this.tokensService.getToken(id, user);

    return new ReadTokenDto(token);
  }

  @CustomPost({
    name: 'Token',
    summary: 'Create a new token',
    bodyType: CreateTokenDto,
    responseType: ReadTokenDto,
    operationId: 'createTokenApi',
  })
  @JwtAuth()
  async createTokenApi(
    @AuthUser() user: User,
    @Body() createTokenDto: CreateTokenDto,
  ) {
    const hash = crypto
      .createHash('sha256')
      .update(crypto.randomBytes(64).toString('base64'))
      .digest('hex');

    const token = await this.tokensService.createToken(
      { ...createTokenDto, hash },
      user,
    );

    return token;
  }

  @CustomDelete({
    name: 'Token',
    summary: 'Delete the specified token',
    operationId: 'deleteTokenApi',
  })
  @JwtAuth()
  async deleteTokenApi(@Param('id') id: string, @AuthUser() user: User) {
    await this.tokensService.deleteToken(id, user);
  }
}
