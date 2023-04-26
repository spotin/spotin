/* eslint-disable @typescript-eslint/ban-types */
import { UnconfiguredSpotAuthGuard } from '@/auth/unconfigured-spot/unconfigured-spot-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiForbiddenResponse, ApiUnauthorizedResponse } from '@nestjs/swagger';

export const UnconfiguredSpotAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(UnconfiguredSpotAuthGuard, ...guards),
    ApiUnauthorizedResponse({
      description: 'The spot is already configured',
    }),
    ApiForbiddenResponse({
      description:
        'Unsufficient roles, permissions or the spot is already configured',
    }),
  );
