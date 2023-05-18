import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { ApiOkResponse, ApiOperation } from '@nestjs/swagger';
import { Response } from 'express';
import {
  Body,
  Controller,
  Get,
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
import { ApiExcludeController } from '@nestjs/swagger';

@ApiExcludeController()
@Controller('auth')
@UseFilters(ViewUnauthorizedExceptionFilter)
export class AuthViewsController {
  constructor(
    private authService: AuthService,
    private usersService: UsersService,
  ) {}

  @Get('login')
  @Render('auth/login')
  renderLoginView() {
    return;
  }

  @Post('login')
  @LocalAuth()
  async login(@AuthUser() user: User, @Res() res: Response) {
    const jwt = await this.authService.generateJwt(user);

    res.cookie(JWT_AUTH_KEY, jwt.jwt, {
      httpOnly: true,
      sameSite: true,
      secure: true,
    });

    res.redirect('/spots');
  }

  @Get('signup')
  @Render('auth/signup')
  renderSignupView() {
    return;
  }

  @Post('signup')
  async signup(@Body() signupUserDto: SignupUserDto, @Res() res: Response) {
    await this.usersService.createUser(signupUserDto);

    res.redirect('/auth/signup');
  }

  @Get('profile')
  @JwtAuth()
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
