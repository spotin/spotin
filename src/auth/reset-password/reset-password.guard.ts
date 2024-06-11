import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class ResetPasswordAuthGuard extends AuthGuard(
	PassportStrategy.RESET_PASSWORD,
) {}
