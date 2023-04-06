import { Controller, Post, Res, Body, HttpCode } from '@nestjs/common';
import { Response } from 'express';
import {
  ApiBody,
  ApiConflictResponse,
  ApiOkResponse,
  ApiOperation,
  ApiTags,
} from '@nestjs/swagger';
import { AuthService } from './auth.service';
import { LoginUserDto } from './dtos/login-user.dto';
import { SignupUserDto } from './dtos/signup-user.dto';
import { UsersService } from '../users/users.service';
import { LocalAuth } from './local/local-auth.decorator';
import { AuthUser } from './decorators/auth-user.decorator';
import { User } from '@prisma/client';
import { JwtAccessTokenDto } from './dtos/jwt-access-token.dto';

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
    summary: 'Log in to SpotIn with username and password',
    description: 'Log in to SpotIn with username and password.',
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
      secure: true,
    });

    // Return json web token
    return res.json(new JwtAccessTokenDto(jwt));
  }

  @Post('signup')
  @HttpCode(200)
  @ApiOperation({
    summary: 'Sign up to SpotIn',
    description: 'Sign up to SpotIn.',
    operationId: 'signup',
  })
  @ApiBody({
    description: "The user's details.",
    type: () => SignupUserDto,
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
