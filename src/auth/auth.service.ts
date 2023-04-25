import * as argon2 from 'argon2';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { User } from '@prisma/client';
import { UsersService } from '@/users/users.service';
import { LoginUser } from '@/auth/types/login-user.type';
import { JwtAccessToken } from '@/auth/types/jwt-access-token';
import { JwtPayload } from '@/auth/types/jwt-payload';

@Injectable()
export class AuthService {
  constructor(
    private usersService: UsersService,
    private jwtService: JwtService,
  ) {}

  async validateCredentials({ username, password }: LoginUser): Promise<User> {
    const user = (await this.usersService.getUserByUsername(username)) as User;

    const passwordsMatch = await argon2.verify(user.password, password);

    if (!user.enabled || !passwordsMatch) {
      throw new UnauthorizedException();
    }

    return user;
  }

  async generateJwtAccessToken(user: User): Promise<JwtAccessToken> {
    const payload: JwtPayload = {
      sub: user.id,
      username: user.username,
      email: user.email,
      role: user.role,
    };

    return {
      accessToken: await this.jwtService.signAsync(payload),
    };
  }

  async validateJwtPayload({ sub }: JwtPayload): Promise<User> {
    const user = (await this.usersService.getUser(sub)) as User;

    if (!user.enabled) {
      throw new UnauthorizedException();
    }

    return user;
  }

  async validateApiKey(tokenHash: string) {
    const user = (await this.usersService.getUserByTokenHash(
      tokenHash,
    )) as User;

    if (!user.enabled) {
      throw new UnauthorizedException();
    }

    return user;
  }
}
