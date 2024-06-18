import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class UnconfiguredSpotAuthGuard extends AuthGuard(
	PassportStrategy.UNCONFIGURED_SPOT,
) {}
