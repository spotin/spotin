/* eslint-disable @typescript-eslint/ban-types */
import { UnconfiguredSpotAuthGuard } from '@/auth/unconfigured-spot/unconfigured-spot-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiForbiddenResponse, ApiUnauthorizedResponse } from '@nestjs/swagger';

export const UnconfiguredSpotAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(UnconfiguredSpotAuthGuard, ...guards),
    ApiUnauthorizedResponse({
      description:
        'The spot is already configured or you are not the owner of the spot.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
