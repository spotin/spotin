import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { ViewUnauthorizedExceptionFilter } from '@/common/filters/view-unauthorized-exception.filter';
import { ReadTokenDto } from '@/tokens/dto/read-token.dto';
import { TokensService } from '@/tokens/tokens.service';
import { Controller, Get, Param, Render, UseFilters } from '@nestjs/common';
import {
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { User } from '@prisma/client';

@ApiTags('Views')
@Controller('tokens')
export class TokensViewsController {
  constructor(private readonly tokensService: TokensService) {}

  @Get()
  @JwtAuth()
  @UseFilters(ViewUnauthorizedExceptionFilter)
  @ApiOperation({
    summary: 'Render the tokens page',
    description: 'Render the tokens page.',
    operationId: 'getTokensView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/index')
  async getTokensView(@AuthUser() user: User) {
    const tokens = await this.tokensService.getTokens(user);

    const tokensDto = tokens.map((token) => new ReadTokenDto(token));

    return {
      title: 'Tokens - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      tokens: tokensDto,
    };
  }

  @Get('create')
  @JwtAuth()
  @UseFilters(ViewUnauthorizedExceptionFilter)
  @ApiOperation({
    summary: 'Render the create a new token page',
    description: 'Render the create a new token page.',
    operationId: 'createTokenView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/form')
  createTokenView(@AuthUser() user: User) {
    return {
      title: 'Create a new token - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
    };
  }

  @Get(':id/delete')
  @JwtAuth()
  @UseFilters(ViewUnauthorizedExceptionFilter)
  @ApiOperation({
    summary: 'Delete the specified token and render the list of token',
    description: 'Delete the specified token and render the list of token.',
    operationId: 'deleteTokenView',
  })
  @ApiParam({
    name: 'id',
    description: 'The token ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/index')
  async deleteTokenView(@AuthUser() user: User, @Param('id') id: string) {
    await this.tokensService.deleteToken(id);

    const tokens = await this.tokensService.getTokens(user);

    const tokensDto = tokens.map((token) => new ReadTokenDto(token));

    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
      tokens: tokensDto,
    };
  }

  @Get(':id')
  @JwtAuth()
  @UseFilters(ViewUnauthorizedExceptionFilter)
  @ApiOperation({
    summary: 'Render the token page',
    description: 'Render the token page.',
    operationId: 'tokenView',
  })
  @ApiParam({
    name: 'id',
    description: 'The token ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/view')
  async tokenView(@AuthUser() user: User, @Param('id') id: string) {
    const token = await this.tokensService.getToken(id, user);

    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
      token: new ReadTokenDto(token),
    };
  }

  @Get('view/:hash/:name')
  @JwtAuth()
  @UseFilters(ViewUnauthorizedExceptionFilter)
  @ApiOperation({
    summary: 'Render the create a new token page',
    description: 'Render the create a new token page.',
    operationId: 'createTokenView',
  })
  @ApiParam({
    name: 'hash',
    description: 'The token hash.',
    format: 'string',
  })
  @ApiParam({
    name: 'name',
    description: 'The token name.',
    format: 'string',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/view')
  viewTokenView(
    @AuthUser() user: User,
    @Param('hash') hash: string,
    @Param('name') name: string,
  ) {
    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
      token: {
        hash: hash,
        name: name,
      },
    };
  }
}
