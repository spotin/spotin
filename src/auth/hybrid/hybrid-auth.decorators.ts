/* eslint-disable @typescript-eslint/ban-types */
import { HybridAuthGuard } from '@/auth/hybrid/hybrid-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiCookieAuth,
  ApiForbiddenResponse,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const HybridAuth = (
  ...guards: (Function | CanActivate)[]
): (<TFunction extends Function, Y>(
  target: object | TFunction,
  propertyKey?: string | symbol | undefined,
  descriptor?: TypedPropertyDescriptor<Y> | undefined,
) => void) =>
  applyDecorators(
    UseGuards(HybridAuthGuard, ...guards),
    ApiCookieAuth(),
    ApiUnauthorizedResponse({
      description: 'Wrong access token.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
