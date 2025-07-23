import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class EmailPasswordAuthGuard extends AuthGuard(
	PassportStrategy.EMAIL_PASSWORD,
) {}
