import { UNCONFIGURED_SPOT_AUTH_KEY } from '@/auth/unconfigured-spot/unconfigured-spot.strategy';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class UnconfiguredSpotAuthGuard extends AuthGuard(
  UNCONFIGURED_SPOT_AUTH_KEY,
) {}
