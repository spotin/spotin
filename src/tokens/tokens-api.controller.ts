import { Controller, Body, Param } from '@nestjs/common';
import { Post } from '@/common/decorators/post.decorator';
import { TokensService } from './tokens.service';
import { CreateTokenDto } from './dto/create-token.dto';
import { ApiTags } from '@nestjs/swagger';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { User } from '@prisma/client';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { ReadTokenDto } from '@/tokens/dto/read-token.dto';
import { UpdateTokenDto } from '@/tokens/dto/update-token.dto';
import { Patch } from '@/common/decorators/patch.decorator';
import * as crypto from 'crypto';

@ApiTags('Tokens')
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
  async getTokenApi(@AuthUser() user: User, @Param('id') id: string) {
    const token = await this.tokensService.getToken(id, user);

    return new ReadTokenDto(token);
  }

  @Post({
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

    return new ReadTokenDto(token);
  }

  @Patch({
    name: 'Token',
    summary: 'Update the specified token',
    bodyType: UpdateTokenDto,
    responseType: ReadTokenDto,
    operationId: 'updateTokenApi',
  })
  @JwtAuth()
  async updateTokenApi(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateTokenDto: UpdateTokenDto,
  ) {
    const token = await this.tokensService.updateToken(id, updateTokenDto);

    return new ReadTokenDto(token);
  }
}
