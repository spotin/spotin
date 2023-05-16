import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { UNRESTRICTED_AUTH_KEY } from '@/auth/unrestricted/unrestricted.strategy';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class JwtOrUnregistrictedAuthGuard extends AuthGuard([
  JWT_AUTH_KEY,
  UNRESTRICTED_AUTH_KEY,
]) {}
