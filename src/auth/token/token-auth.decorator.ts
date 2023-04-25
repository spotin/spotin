/* eslint-disable @typescript-eslint/ban-types */
import { ApiKeyAuthGuard } from '@/auth/token/token-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';

export const ApiKeyAuth = (
  ...guards: (Function | CanActivate)[]
): (<TFunction extends Function, Y>(
  target: object | TFunction,
  propertyKey?: string | symbol | undefined,
  descriptor?: TypedPropertyDescriptor<Y> | undefined,
) => void) =>
  applyDecorators(
    UseGuards(ApiKeyAuthGuard, ...guards),
    ApiSecurity('apiKey'),
    ApiUnauthorizedResponse({
      description: 'Wrong token.',
    }),
  );
