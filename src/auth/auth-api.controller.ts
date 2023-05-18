import { Controller, Post, Res, Body, HttpCode, Get } from '@nestjs/common';
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
import { JwtDto } from '@/auth/dtos/jwt.dto';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';

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
    type: JwtDto,
  })
  async login(@AuthUser() user: User) {
    const jwt = await this.authService.generateJwt(user);

    return new JwtDto(jwt);
  }

  @Get('logout')
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
  async logout(@Res({ passthrough: true }) res: Response) {
    res.clearCookie(JWT_AUTH_KEY);
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
