import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { Controller, Get, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';
import { User } from '@prisma/client';

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
      username: user?.username,
      email: user?.email,
      role: user?.role,
    };
  }
}
