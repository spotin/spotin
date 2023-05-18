import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { Response } from 'express';
import {
  Body,
  Controller,
  Get,
  HttpCode,
  Post,
  Render,
  Res,
  UseFilters,
} from '@nestjs/common';
import { User } from '@prisma/client';
import { ViewUnauthorizedExceptionFilter } from '@/common/filters/view-unauthorized-exception.filter';
import { LocalAuth } from '@/auth/local/local-auth.decorator';
import { AuthService } from '@/auth/auth.service';
import { SignupUserDto } from '@/auth/dtos/signup-user.dto';
import { UsersService } from '@/users/users.service';
import {
  ApiOperation,
  ApiOkResponse,
  ApiBody,
  ApiConflictResponse,
  ApiBadRequestResponse,
  ApiTags,
} from '@nestjs/swagger';
import { LoginUserDto } from '@/auth/dtos/login-user.dto';
import { MissingOrIncorrectFieldsResponse } from '@/common/openapi/responses';

@ApiTags('Auth - Views')
@Controller('auth')
@UseFilters(ViewUnauthorizedExceptionFilter)
export class AuthViewsController {
  constructor(
    private authService: AuthService,
    private usersService: UsersService,
  ) {}

  @Get('login')
  @ApiOperation({
    summary: 'Render the login page',
    description: 'Render the login page.',
    operationId: 'renderLoginView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('auth/login')
  renderLoginView() {
    return;
  }

  @Post('login')
  @LocalAuth()
  @HttpCode(200)
  @ApiOperation({
    summary: 'Log in to Spot in with username and password',
    description:
      'Log in to Spot in with username and password. Save the JWT in Cookie and redirect to `/spots`.',
    operationId: 'loginView',
  })
  @ApiBody({
    description: "The user's credentials.",
    type: LoginUserDto,
  })
  @ApiOkResponse({
    description: 'The user has been successfully logged in.',
  })
  @ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
  async loginView(@AuthUser() user: User, @Res() res: Response) {
    const jwt = await this.authService.generateJwt(user);

    res.cookie(JWT_AUTH_KEY, jwt.jwt, {
      httpOnly: true,
      sameSite: true,
      secure: true,
    });

    res.redirect('/spots');
  }

  @Get('signup')
  @ApiOperation({
    summary: 'Render the signup page',
    description: 'Render the signup page.',
    operationId: 'renderSignupView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('auth/signup')
  renderSignupView() {
    return;
  }

  @Post('signup')
  @HttpCode(200)
  @ApiOperation({
    summary: 'Sign up to Spot in',
    description: 'Sign up to Spot in.',
    operationId: 'signupView',
  })
  @ApiBody({
    description: "The user's details.",
    type: SignupUserDto,
  })
  @ApiOkResponse({
    description: 'The user has been successfully signed up.',
  })
  @ApiConflictResponse({
    description: 'Another user has the same username.',
  })
  @ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
  async signupView(@Body() signupUserDto: SignupUserDto, @Res() res: Response) {
    await this.usersService.createUser(signupUserDto);

    res.redirect('/auth/signup');
  }

  @Get('profile')
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the profile page',
    description: 'Render the profile page.',
    operationId: 'renderProfileView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('auth/profile')
  renderProfileView(@AuthUser() user: User) {
    return {
      username: user.username,
      email: user.email,
      role: user.role,
    };
  }

  @Get('logout')
  @JwtAuth()
  @ApiOperation({
    summary: 'Log out from Spot in',
    description:
      'Log out from Spot in. Clear the JWT Cookie and redirect to `/`.',
    operationId: 'logoutView',
  })
  @ApiOkResponse({
    description: 'The user has been successfully logged in.',
  })
  logoutView(@Res() res: Response) {
    res.clearCookie(JWT_AUTH_KEY);

    res.redirect('/');
  }
}
