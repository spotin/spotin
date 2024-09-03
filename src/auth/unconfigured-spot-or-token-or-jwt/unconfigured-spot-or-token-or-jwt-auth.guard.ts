import { PassportStrategy } from '@/auth/auth.constants';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class UnconfiguredSpotOrTokenOrJwtAuthGuard extends AuthGuard([
	PassportStrategy.UNCONFIGURED_SPOT,
	PassportStrategy.TOKEN,
	PassportStrategy.JWT,
]) {}
