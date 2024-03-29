import { HeaderAPIKeyStrategy } from 'passport-headerapikey';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';
import { TOKEN_HEADER_NAME, PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { User } from '@prisma/client';

type DoneCallback = (
  err: Error | null,
  user?: User | false,
  info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
  strategy: PASSPORT_STRATEGY.TOKEN,
};

@Injectable()
export class TokenStrategy extends PassportStrategy(
  HeaderAPIKeyStrategy,
  PASSPORT_STRATEGY.TOKEN,
) {
  constructor(private authService: AuthService) {
    // Signature from http://www.passportjs.org/packages/passport-headerapikey/
    super(
      { header: TOKEN_HEADER_NAME },
      false,
      async (value: string, done: DoneCallback) => {
        try {
          const user = await this.authService.validateToken(value);

          done(null, user, authInfo);
        } catch (error) {
          done(null, false, authInfo);
        }
      },
    );
  }
}
