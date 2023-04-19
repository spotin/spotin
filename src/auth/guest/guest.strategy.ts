import { Strategy } from 'passport-jwt';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { Request } from 'express';
import { User } from '@prisma/client';
import { JwtPayload } from '@/auth/types/jwt-payload';
import { AuthService } from '@/auth/auth.service';
import { ConfigService } from '@nestjs/config';
import { JWT_SECRET } from '@/config/config.constants';

@Injectable()
export class GuestStrategy extends PassportStrategy(Strategy, 'guest') {
  constructor(private authService: AuthService, configService: ConfigService) {
    super({
      jwtFromRequest: (request: Request) => request.cookies.accessToken,
      ignoreExpiration: false,
      secretOrKey: configService.get(JWT_SECRET, { infer: true }),
    });
  }

  async validate(payload: JwtPayload): Promise<User | null> {
    console.log('AHHHHH');
    try {
      const user = await this.authService.validateJwtPayload(payload);

      return user;
    } catch (error) {
      return null;
    }
  }
}
