import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { Injectable } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { User } from '@prisma/client';
import { Strategy } from 'passport-custom';

type DoneCallback = (
  err: Error | null,
  user?: User | null | true,
  info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
  strategy: PASSPORT_STRATEGY.UNRESTRICTED,
};

@Injectable()
export class UnrestrictedStrategy extends PassportStrategy(
  Strategy,
  PASSPORT_STRATEGY.UNRESTRICTED,
) {
  constructor() {
    // Signature from https://www.passportjs.org/packages/passport-custom/
    super((_: Request, done: DoneCallback) => {
      done(null, true, authInfo);
    });
  }
}
