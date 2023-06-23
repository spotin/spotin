import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { AuthService } from '@/auth/auth.service';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { User, UserRole } from '@prisma/client';
import { Request } from 'express';
import { Strategy } from 'passport-custom';

type DoneCallback = (
  err: Error | null,
  user?: Pick<User, 'id' | 'role'>,
  info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
  strategy: PASSPORT_STRATEGY.UNCONFIGURED_SPOT,
};

@Injectable()
export class UnconfiguredSpotStrategy extends PassportStrategy(
  Strategy,
  PASSPORT_STRATEGY.UNCONFIGURED_SPOT,
) {
  constructor(private authService: AuthService) {
    // Signature from https://www.passportjs.org/packages/passport-custom/
    super(async (request: Request, done: DoneCallback) => {
      try {
        const spotId = request.params.id;

        const user = await this.authService.validateSpot(spotId);

        // Only keep the owner's ID and switch the role to GUEST
        done(
          null,
          {
            id: user.id,
            role: UserRole.GUEST,
          },
          authInfo,
        );
      } catch {
        done(new UnauthorizedException());
      }
    });
  }
}
