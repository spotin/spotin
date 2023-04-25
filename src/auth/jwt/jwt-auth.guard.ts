import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class JwtAuthGuard extends AuthGuard(JWT_AUTH_KEY) {}
