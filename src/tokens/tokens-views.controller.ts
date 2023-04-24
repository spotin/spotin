import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { TokensService } from '@/tokens/tokens.service';
import { Controller, Get, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';
import { User } from '@prisma/client';

@ApiTags('Views')
@Controller('tokens')
export class TokensViewsController {
  constructor(private readonly tokenService: TokensService) {}

  @Get()
  @JwtAuth()
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
    const tokens = await this.tokenService.getTokens(user);

    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
      tokens,
    };
  }

  @Get('create')
  @ApiOperation({
    summary: 'Render the create a new token page',
    description: 'Render the create a new token page.',
    operationId: 'createTokenView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/form')
  @JwtAuth()
  createTokenView(@AuthUser() user: User) {
    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
    };
  }
}
