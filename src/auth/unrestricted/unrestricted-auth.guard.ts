import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class UnrestrictedAuthGuard extends AuthGuard(
	PassportStrategy.UNRESTRICTED,
) {}
