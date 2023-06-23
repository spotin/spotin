import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class UnconfiguredSpotOrTokenOrJwtAuthGuard extends AuthGuard([
  PASSPORT_STRATEGY.UNCONFIGURED_SPOT,
  PASSPORT_STRATEGY.TOKEN,
  PASSPORT_STRATEGY.JWT,
]) {}
