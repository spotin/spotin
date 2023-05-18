import { HeaderAPIKeyStrategy } from 'passport-headerapikey';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';
import { User } from '@prisma/client';

export const TOKEN_AUTH_KEY = 'token';

@Injectable()
export class TokenStrategy extends PassportStrategy(
  HeaderAPIKeyStrategy,
  TOKEN_AUTH_KEY,
) {
  constructor(private authService: AuthService) {
    super({ header: TOKEN_AUTH_KEY }, false);
  }

  async validate(token: string): Promise<User> {
    try {
      const user = await this.authService.validateToken(token);

      return user;
    } catch (error) {
      throw new UnauthorizedException();
    }
  }
}
