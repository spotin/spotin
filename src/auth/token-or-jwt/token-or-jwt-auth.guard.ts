import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class TokenOrJwtAuthGuard extends AuthGuard([
  PASSPORT_STRATEGY.TOKEN,
  PASSPORT_STRATEGY.JWT,
]) {}
