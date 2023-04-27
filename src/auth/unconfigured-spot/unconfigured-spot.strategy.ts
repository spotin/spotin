import { AuthService } from '@/auth/auth.service';
import { Injectable } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { User, Spot } from '@prisma/client';
import { Strategy } from 'passport-custom';

export const UNCONFIGURED_SPOT_AUTH_KEY = 'unconfigured-spot';

@Injectable()
export class UnconfiguredSpotStrategy extends PassportStrategy(
  Strategy,
  UNCONFIGURED_SPOT_AUTH_KEY,
) {
  constructor(private authService: AuthService) {
    super(
      async (
        request: { params: { id: any } },
        verify: (err: Error | unknown, verified?: boolean | User) => void,
      ) => {
        try {
          const spotId = request.params.id;

          const user = await this.authService.validateSpot(spotId);

          verify(null, user);
        } catch {
          verify(null, false);
        }
      },
    );
  }
}
