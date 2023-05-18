import { UNRESTRICTED_AUTH_KEY } from '@/auth/unrestricted/unrestricted.strategy';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class UnrestrictedAuthGuard extends AuthGuard(UNRESTRICTED_AUTH_KEY) {}
