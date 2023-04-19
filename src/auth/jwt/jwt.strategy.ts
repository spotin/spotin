import { Strategy } from 'passport-jwt';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { Request } from 'express';
import { User } from '@prisma/client';
import { JwtPayload } from '@/auth/types/jwt-payload';
import { AuthService } from '@/auth/auth.service';
import { ConfigService } from '@nestjs/config';
import { JWT_SECRET } from '@/config/config.constants';

@Injectable()
export class JwtStrategy extends PassportStrategy(Strategy, 'jwt') {
  constructor(private authService: AuthService, configService: ConfigService) {
    super({
      jwtFromRequest: (request: Request) => request.cookies.accessToken,
      ignoreExpiration: false,
      secretOrKey: configService.get(JWT_SECRET, { infer: true }),
    });
  }

  async validate(payload: JwtPayload): Promise<User> {
    try {
      const user = await this.authService.validateJwtPayload(payload);

      return user;
    } catch (error) {
      throw new UnauthorizedException();
    }
  }
}
