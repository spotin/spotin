import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { ViewUnauthorizedExceptionFilter } from '@/common/filters/view-unauthorized-exception.filter';
import { Controller, Get, Render, Res, UseFilters } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';
import { User } from '@prisma/client';
import { Response } from 'express';

@ApiTags('Views')
@Controller('auth')
export class AuthViewsController {
  @Get('login')
  @ApiOperation({
    summary: 'Render the login page',
    description: 'Render the login page.',
    operationId: 'loginView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('auth/login')
  loginView() {
    return;
  }

  @Get('signup')
  @ApiOperation({
    summary: 'Render the sign up page',
    description: 'Render the sign up page.',
    operationId: 'signupView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('auth/signup')
  signupView() {
    return;
  }

  @Get('profile')
  @JwtAuth()
  @UseFilters(ViewUnauthorizedExceptionFilter)
  @ApiOperation({
    summary: 'Render the profile page',
    description: 'Render the profile page.',
    operationId: 'profileView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('auth/profile')
  profileView(@AuthUser() user: User) {
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
