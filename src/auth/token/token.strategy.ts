import { HeaderAPIKeyStrategy } from 'passport-headerapikey';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';

@Injectable()
export class ApiKeyStrategy extends PassportStrategy(
  HeaderAPIKeyStrategy,
  'apiKey',
) {
  constructor(private authService: AuthService) {
    super(
      { header: 'apiKey' },
      false,
      async (
        apiKey: string,
        verify: (err: Error | unknown, verified?: boolean) => void,
      ) => {
        try {
          await this.authService.validateApiKey(apiKey);
          verify(null, true);
        } catch (error) {
          verify(null, false);
        }
      },
    );
  }
}
