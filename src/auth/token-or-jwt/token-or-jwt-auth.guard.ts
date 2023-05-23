import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { TOKEN_AUTH_KEY } from '@/auth/token/token.strategy';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class TokenOrJwtAuthGuard extends AuthGuard([
  TOKEN_AUTH_KEY,
  JWT_AUTH_KEY,
]) {}
