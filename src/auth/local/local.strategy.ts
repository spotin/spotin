import { Strategy } from 'passport-local';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { User } from '@prisma/client';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';

type DoneCallback = (
  err: Error | null,
  user?: User | false,
  info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
  strategy: PASSPORT_STRATEGY.LOCAL,
};

@Injectable()
export class LocalStrategy extends PassportStrategy(
  Strategy,
  PASSPORT_STRATEGY.LOCAL,
) {
  constructor(private authService: AuthService) {
    // Signature from https://www.passportjs.org/packages/passport-local/
    super(async (username: string, password: string, done: DoneCallback) => {
      try {
        const user = await this.authService.validateCredentials({
          username,
          password,
        });

        done(null, user, authInfo);
      } catch (error) {
        done(null, false, authInfo);
      }
    });
  }
}
