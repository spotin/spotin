import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class TokenOrJwtAuthGuard extends AuthGuard([
	PassportStrategy.TOKEN,
	PassportStrategy.JWT,
]) {}
