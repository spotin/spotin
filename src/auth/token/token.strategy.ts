import { HeaderAPIKeyStrategy } from 'passport-headerapikey';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';

@Injectable()
export class TokenStrategy extends PassportStrategy(
  HeaderAPIKeyStrategy,
  'token',
) {
  constructor(private authService: AuthService) {
    super(
      { header: 'token' },
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
