import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { TOKEN_AUTH_KEY } from '@/auth/token/token.strategy';
import { UNCONFIGURED_SPOT_AUTH_KEY } from '@/auth/unconfigured-spot/unconfigured-spot.strategy';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class UnconfiguredSpotOrTokenOrJwtAuthGuard extends AuthGuard([
  UNCONFIGURED_SPOT_AUTH_KEY,
  TOKEN_AUTH_KEY,
  JWT_AUTH_KEY,
]) {}
