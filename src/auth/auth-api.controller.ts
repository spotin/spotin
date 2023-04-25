import { Controller, Post, Res, Body, HttpCode } from '@nestjs/common';
import { Response } from 'express';
import {
  ApiBody,
  ApiConflictResponse,
  ApiNoContentResponse,
  ApiOkResponse,
  ApiOperation,
  ApiTags,
} from '@nestjs/swagger';
import { User } from '@prisma/client';
import { AuthService } from '@/auth/auth.service';
import { LoginUserDto } from '@/auth/dtos/login-user.dto';
import { SignupUserDto } from '@/auth/dtos/signup-user.dto';
import { UsersService } from '@/users/users.service';
import { LocalAuth } from '@/auth/local/local-auth.decorator';
import { JwtAccessTokenDto } from '@/auth/dtos/jwt-access-token.dto';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';

@ApiTags('Auth')
@Controller('api/auth')
export class AuthApiController {
  constructor(
    private authService: AuthService,
    private usersService: UsersService,
  ) {}

  @Post('login')
  @LocalAuth()
  @HttpCode(200)
  @ApiOperation({
    summary: 'Log in to Spot in with username and password',
    description: 'Log in to Spot in with username and password.',
    operationId: 'login',
  })
  @ApiBody({
    description: "The user's credentials.",
    type: LoginUserDto,
  })
  @ApiOkResponse({
    description: 'The user has been successfully logged in.',
    type: JwtAccessTokenDto,
  })
  async login(@AuthUser() user: User, @Res() res: Response) {
    const jwt = await this.authService.generateJwtAccessToken(user);

    res.cookie('accessToken', jwt.accessToken, {
      httpOnly: true,
      sameSite: true,
      secure: true,
    });

    // Return json web token
    return res.json(new JwtAccessTokenDto(jwt));
  }

  @Post('logout')
  @JwtAuth()
  @HttpCode(204)
  @ApiOperation({
    summary: 'Log out from Spot in',
    description: 'Log out from Spot in.',
    operationId: 'logout',
  })
  @ApiNoContentResponse({
    description: 'The user has been successfully logged out.',
  })
  async logout(@Res() res: Response) {
    res.cookie('accessToken', '', {
      httpOnly: true,
      sameSite: true,
      secure: true,
    });

    res.end();
  }

  @Post('signup')
  @HttpCode(200)
  @ApiOperation({
    summary: 'Sign up to Spot in',
    description: 'Sign up to Spot in.',
    operationId: 'signup',
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
  async signup(@Body() signupUserDto: SignupUserDto) {
    await this.usersService.createUser(signupUserDto);
  }
}
