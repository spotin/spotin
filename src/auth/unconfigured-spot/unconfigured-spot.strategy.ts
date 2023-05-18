import { AuthService } from '@/auth/auth.service';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { User, UserRole } from '@prisma/client';
import { Request } from 'express';
import { Strategy } from 'passport-custom';

export const UNCONFIGURED_SPOT_AUTH_KEY = 'unconfigured-spot';

@Injectable()
export class UnconfiguredSpotStrategy extends PassportStrategy(
  Strategy,
  UNCONFIGURED_SPOT_AUTH_KEY,
) {
  constructor(private authService: AuthService) {
    super();
  }

  async validate(request: Request): Promise<Pick<User, 'id' | 'role'>> {
    try {
      const spotId = request.params.id;

      const user = await this.authService.validateSpot(spotId);

      // Only keep the owner's ID and switch the role to GUEST
      return {
        id: user.id,
        role: UserRole.GUEST,
      };
    } catch {
      throw new UnauthorizedException();
    }
  }
}
