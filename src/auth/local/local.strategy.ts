import { Strategy } from 'passport-local';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';
import { User } from '@prisma/client';

export const LOCAL_AUTH_KEY = 'local';

@Injectable()
export class LocalStrategy extends PassportStrategy(Strategy, LOCAL_AUTH_KEY) {
  constructor(private authService: AuthService) {
    super();
  }

  async validate(username: string, password: string): Promise<User> {
    try {
      const user = await this.authService.validateCredentials({
        username,
        password,
      });

      return user;
    } catch (error) {
      throw new UnauthorizedException();
    }
  }
}
