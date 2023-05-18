import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { ViewUnauthorizedExceptionFilter } from '@/common/filters/view-unauthorized-exception.filter';
import { CreateTokenDto } from '@/tokens/dto/create-token.dto';
import { ReadTokenDto } from '@/tokens/dto/read-token.dto';
import { TokensService } from '@/tokens/tokens.service';
import {
  Body,
  Controller,
  Delete,
  Get,
  HttpCode,
  HttpStatus,
  Param,
  Post,
  Redirect,
  Render,
  Res,
  Session,
  UseFilters,
} from '@nestjs/common';
import {
  ApiBody,
  ApiCreatedResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { User } from '@prisma/client';
import * as crypto from 'crypto';
import { Response } from 'express';

@ApiTags('Tokens - Views')
@Controller('tokens')
@UseFilters(ViewUnauthorizedExceptionFilter)
export class TokensViewsController {
  constructor(private readonly tokensService: TokensService) {}

  @Get('create')
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the create a new token page',
    description: 'Render the create a new token page.',
    operationId: 'renderTokenView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/form')
  renderTokenView(@AuthUser() user: User) {
    return {
      title: 'Create a new token - Spot in',
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
    operationId: 'getTokensView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/index')
  async getTokensView(@AuthUser() user: User) {
    const tokens = await this.tokensService.getTokens(user);

    return {
      title: 'Tokens - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      tokens,
    };
  }

  @Get(':id')
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the token page',
    description: 'Render the token page.',
    operationId: 'getTokenView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/[id]')
  async getTokenView(
    @AuthUser() user: User,
    @Session() session: Record<string, unknown>,
  ) {
    // Get token from the session
    const token = session.token;

    // Delete token from the session
    delete session.token;

    return {
      title: 'Token - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      token,
    };
  }

  @CustomPost({
    name: 'Token',
    summary: 'Create a new token',
    description: 'Create a new token. Redirect to `/tokens/:id`.',
    bodyType: CreateTokenDto,
    responseType: ReadTokenDto,
    operationId: 'createTokenView',
  })
  @JwtAuth()
  async createTokenView(
    @AuthUser() user: User,
    @Body() createTokenDto: CreateTokenDto,
    @Session() session: Record<string, unknown>,
    @Res() res: Response,
  ) {
    const hash = crypto
      .createHash('sha256')
      .update(crypto.randomBytes(64).toString('base64'))
      .digest('hex');

    const token = await this.tokensService.createToken(
      { ...createTokenDto, hash },
      user,
    );

    // Store the token in the session
    session.token = token;

    // Redirect to the token page
    res.redirect(HttpStatus.FOUND, `/tokens/${token.id}`);
  }

  @Get(':id/delete')
  @JwtAuth()
  @ApiOperation({
    summary: 'Delete the specified token',
    description: 'Delete the specified token. Redirect to `/tokens`.',
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
  @Redirect('/tokens', HttpStatus.FOUND)
  async deleteTokenView(@AuthUser() user: User, @Param('id') id: string) {
    await this.tokensService.deleteToken(id);
  }
}
