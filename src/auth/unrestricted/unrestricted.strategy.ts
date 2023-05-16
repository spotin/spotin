import { Injectable } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { Strategy } from 'passport-custom';

export const UNRESTRICTED_AUTH_KEY = 'unrestricted';

@Injectable()
export class UnrestrictedStrategy extends PassportStrategy(
  Strategy,
  UNRESTRICTED_AUTH_KEY,
) {
  constructor() {
    super(async (_: any, verify: (arg0: null, arg1: boolean) => void) => {
      verify(null, true);
    });
  }
}
