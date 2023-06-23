import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { BadRequestMvcExceptionFilter } from '@/common/filters/bad-request-mvc-exception.filter';
import { NotFoundMvcExceptionFilter } from '@/common/filters/not-found-mvc-exception.filter';
import { UnauthorizedMvcExceptionFilter } from '@/common/filters/unauthorized-mvc-exception.filter';
import { CreateTokenDto } from '@/tokens/dto/create-token.dto';
import { TokensService } from '@/tokens/tokens.service';
import {
  Body,
  Controller,
  Get,
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
  ApiCreatedResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { Token, User } from '@prisma/client';
import * as crypto from 'crypto';
import { Response } from 'express';
import { SessionData } from 'express-session';

@ApiTags('MVC - Tokens')
@Controller('tokens')
@UseFilters(UnauthorizedMvcExceptionFilter)
@UseFilters(BadRequestMvcExceptionFilter)
@UseFilters(NotFoundMvcExceptionFilter)
export class TokensMvcController {
  constructor(private readonly tokensService: TokensService) {}

  @Get('create')
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the create a new token page',
    description: 'Render the create a new token page.',
    operationId: 'renderCreateTokenMvc',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/form')
  renderCreateTokenMvc(
    @AuthUser() user: User,
    @Session() session: SessionData,
  ) {
    // Get errors from the session
    const errors = session.errors;

    // Delete errors from the session
    delete session.errors;

    return {
      title: 'Create a new token - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      errors,
    };
  }

  @Get()
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the tokens page',
    description: 'Render the tokens page.',
    operationId: 'renderTokensListMvc',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('tokens/list')
  async renderTokensListMvc(@AuthUser() user: User) {
    const tokens = await this.tokensService.getTokens(user);

    return {
      title: 'Tokens - Spot in',
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
    operationId: 'deleteTokenMvc',
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
  async deleteTokenMvc(@AuthUser() user: User, @Param('id') id: string) {
    await this.tokensService.deleteToken(id, user);
  }

  @Get(':id')
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the token page',
    description: 'Render the token page.',
    operationId: 'renderTokenMvc',
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
  async renderTokenMvc(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Session() session: SessionData,
  ) {
    let token: Omit<Token, 'hash'> & { hash?: string };

    if (session.token) {
      // Get token from the session
      token = session.token;

      // Delete token from the session
      delete session.token;
    } else {
      token = await this.tokensService.getToken(id, user);

      // Delete the hash
      delete token.hash;
    }

    return {
      title: 'Token - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      token,
    };
  }

  @Post()
  @JwtAuth()
  @ApiOperation({
    summary: 'Create a new token',
    description: 'Create a new token. Redirect to `/tokens/:id`.',
    operationId: 'createTokenMvc',
  })
  @ApiCreatedResponse({
    description: 'Redirect successful.',
  })
  async createTokenMvc(
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
    res.redirect(`/tokens/${token.id}`);
  }
}
