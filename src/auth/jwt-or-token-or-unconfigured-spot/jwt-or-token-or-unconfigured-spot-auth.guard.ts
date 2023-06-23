import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class JwtOrTokenOrUnconfiguredSpotAuthGuard extends AuthGuard([
  PASSPORT_STRATEGY.JWT,
  PASSPORT_STRATEGY.TOKEN,
  PASSPORT_STRATEGY.UNCONFIGURED_SPOT,
]) {}
