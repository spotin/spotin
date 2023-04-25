import { HeaderAPIKeyStrategy } from 'passport-headerapikey';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';

export const TOKEN_AUTH_KEY = 'token';

@Injectable()
export class TokenStrategy extends PassportStrategy(
  HeaderAPIKeyStrategy,
  TOKEN_AUTH_KEY,
) {
  constructor(private authService: AuthService) {
    super(
      { header: TOKEN_AUTH_KEY },
      false,
      async (
        token: string,
        verify: (err: Error | unknown, verified?: boolean) => void,
      ) => {
        try {
          await this.authService.validateToken(token);
          verify(null, true);
        } catch (error) {
          verify(null, false);
        }
      },
    );
  }
}
